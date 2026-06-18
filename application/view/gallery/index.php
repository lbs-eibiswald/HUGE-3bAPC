<div class="container">
    <h1>Up- and Download Images</h1>
    <div class="box">

        <!-- echo out the system feedback (error and success messages) -->
        <?php $this->renderFeedbackMessages(); ?>

        <div class="upload-image-container">
            <form action="<?php echo Config::get('URL'); ?>gallery/prepareImageUpload" method="post" enctype="multipart/form-data">
                Select image to upload:
                <input type="file" name="fileUpload" id="fileUpload"> <!-- accept=".png, .jpg, .jpeg, .webpb, .svg" -->
                <input type="submit" value="Upload Image" name="submit">
            </form>
        </div>

        <div class="gallery-container">
            <?php
                if (!empty($this->images)) {
                    foreach($this->images as $image) {
                        $filename = $image->name;
                        $imageURL = Config::get('URL') . 'gallery/showImage/' . urlencode($filename);
            ?>

            <div class="gallery-item">
                <a href="<?php echo $imageURL; ?>" target="_blank">
                    <img src="<?php echo $imageURL; ?>" alt="Gallery Image" />
                </a>

                <div class="image-info">
                    <div class="image-actions">
                        <form action="<?php echo Config::get('URL'); ?>gallery/downloadImage" method="post">
                            <input type="hidden" name="filename" value="<?php echo $filename; ?>">
                            <button type="submit">Download Image</button>
                        </form>

                        <form action="<?php echo Config::get('URL'); ?>gallery/deleteImage" method="post">
                            <input type="hidden" name="filename" value="<?php echo $filename; ?>">
                            <button type="submit">Delete Image</button>
                        </form>

                        <p>Downloads: <?php echo $image->downloads; ?></p>
                    </div>
                </div>
            </div>

            <?php
                    }
                } else {
                    echo 'No images';
                }
            ?>
        </div>
    </div>
</div>