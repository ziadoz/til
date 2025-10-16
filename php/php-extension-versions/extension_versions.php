<?php
foreach (get_loaded_extensions() as $extension) {
    echo "$extension: " . phpversion($extension) . "\n";
}