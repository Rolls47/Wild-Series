<?php


namespace App\Service;


class Slugify
{

    public function generate(string $input) : string
    {
        $slugInput = mb_strtolower($input);
        $slugInput = strtolower($slugInput);
        $slugInput = preg_replace('/\s+/', '-', $slugInput);
        $slugInput = preg_replace('/[^a-z0-9-]/', '', $slugInput);
        $slugInput = trim($slugInput);




        return  $slugInput;

    }

}
