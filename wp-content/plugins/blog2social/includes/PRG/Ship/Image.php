<?php

class PRG_Ship_Image {

    public function __construct() {
         
    }

   public function getItemHtml($postId,$postContent,$postUrl,$userLang='en') {
        $content = '';
        $imageData = B2S_Util::getImagesByPostID($postId,$postContent,$postUrl, true,$userLang);
        $isImage = (is_array($imageData) && !empty($imageData)) ? true : false;

        if ($isImage) {
            $tempCountImage = 0;
            foreach ($imageData as $key => $image) {
                $content .='<div class="prg-image-item">';
                $content .='<div class="prg-image-item-thumb">';
                $content .='<label for="prg-image-count-' . $tempCountImage . '">';
                $content .='<img class="img-thumbnail" alt="blogImage" src="' . $image[0] . '">';
                $content .= '</label>';
                $content .='</div>';
                $content .='<div class="prg-image-item-caption text-center">';
                $content .= '<input  ' . (($tempCountImage == 0) ? 'checked="checked"' : '') . ' type="radio" value="' . $image[0] . '" name="bild" class="prgImage" id="prg-image-count-' . $tempCountImage . '">';
                $content .= '</div>';
                $content .= '</div>';
                $tempCountImage++;
            }
        }

        return $content;
    }

}
