<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\ProductStock;
use Intervention\Image\Facades\Image;

class ProcessProductvariations extends Command
{

    // Command signature
    protected $signature = 'process:product-variations';

    // Command description
    protected $description = 'Process and convert all product variations to WebP format and resize them.';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        // Remove execution time limit
        set_time_limit(0);

        // Fetch all products that have a thumbnail
        $products = ProductStock::select('id', 'image')->get();

        // Path to the image directory
        $imageDirectory = public_path('upload/products/variations');
        $directoryImages = glob($imageDirectory . '/*');

        $imagesToProcess = [];

        // Identify images to process
        foreach ($products as $product) {
            $storedImagePath = public_path($product->image);
            if (in_array($storedImagePath, $directoryImages)) {
                $imagesToProcess[] = [
                    'product' => $product,
                    'old_image' => $storedImagePath
                ];
            }
        }

        // Process each image
        foreach ($imagesToProcess as $item) {
            $product = $item['product'];
            $oldImage = $item['old_image'];
            if (file_exists($oldImage)) {
                // Resize and convert image to WebP, delete old image, and update DB
                $this->processImage($product, $oldImage);
            }
        }

        // Display success message
        $this->info('All product thumbnails have been successfully processed.');
    }

    protected function processImage($product, $oldImage)
    {
        // Validate the input image
        if (!file_exists($oldImage)) {
            throw new \Exception("Image file does not exist: " . $oldImage);
        }

        // Load the image
        $image = Image::make($oldImage);

        // Resize the image to fit within 900x1200 while maintaining the aspect ratio
        $image->resize(900, 1200, function ($constraint) {
            $constraint->aspectRatio(); // Maintain aspect ratio
            $constraint->upsize();     // Prevent upsizing smaller images
        });

        // Define target size and initialize quality
        $targetSizeKB = 25; // Target file size in KB
        $quality = 100;     // Start with maximum quality
        $tempPath = tempnam(sys_get_temp_dir(), 'image') . '.webp';

        do {
            // Encode and save the image with the current quality
            $image->encode('webp', $quality)->save($tempPath);

            // Calculate file size
            $fileSizeKB = filesize($tempPath) / 1024; // Convert bytes to KB
            $quality -= 5; // Reduce quality incrementally if size exceeds the target

        } while ($fileSizeKB > $targetSizeKB && $quality > 5);

        // Prepare the new image path
        $newImagePath = pathinfo($oldImage, PATHINFO_DIRNAME) . '/' . pathinfo($oldImage, PATHINFO_FILENAME) . '.webp';

        // Move the compressed image to the new path
        rename($tempPath, $newImagePath);

        // Remove the old image if it exists and is different from the new image
        if (file_exists($oldImage) && $oldImage !== $newImagePath) {
            unlink($oldImage);
        }

        // Update the product thumbnail path in the database
        $product->image = 'upload/products/variations/' . pathinfo($oldImage, PATHINFO_FILENAME) . '.webp';
        $product->save();
    }





}
