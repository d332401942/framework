<?php

class CacheVendorLib extends Feng {

    public function startCache() {
        ob_start();
    }

    public function endCache() {
        $content = ob_get_contents();
        ob_end_clean();
        P($content);
    }

}
