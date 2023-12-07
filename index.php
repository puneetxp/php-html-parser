<?php

use The\PHPHtmlParser\Cleaner;
use The\PHPHtmlParser\Smart;

include_once "./vendor/autoload.php";

$string = '
<t-l.base :title=$service["name"] :description=$service["seo_description"]>
    <app-service>
        <header t-if="$condtion" class="flex px-0 text-white bg-mat_blue">
            <div class="grid w-full py-3">
                <h1 class="pt-6 px-2 text-center">{{$service["name"]}} Compliances </h1>
                <h2 class="text-center px-8 md:px-6">{{$service["name"]}} Compliances</h2>
                <span class="flex justify-center pb-6">
                    <button mat-button=""
                            onclick=' . "'" . 'document.getElementById("services").scrollIntoView({behavior: "smooth", block: "start", inline: "start"})' . "'" . '
                            target="_blank" mat-raised-button=""
                            class="w-52 mdc-button mat-mdc-button mdc-button--raised mat-mdc-raised-button mat-unthemed mat-mdc-button-base"
                            mat-ripple-loader-class-name="mat-mdc-button-ripple">
                        <span class="mat-mdc-button-persistent-ripple mdc-button__ripple"></span>
                        <span class="mdc-button__label"> Check {{$service["short_name"]}} Services</span>
                        <span class="mat-mdc-focus-indicator"></span>
                        <span class="mat-mdc-button-touch-target"></span>
                        <span class="mat-ripple mat-mdc-button-ripple"></span>
                    </button>
                    <p>
                    djfije
                    <p>
                </span>
                <h2 class="text-justify m-auto max-w-4xl px-4">{{$service["short_description"]}}</h2>
            </div>
            <div>
        </header>
    </app-service>
</t-l.base>
<script>
    var x= "";
</script>';

// echo $dom->loadStr($string);



print_r((new Smart((new Cleaner())->clean($string)))->parse()->tags);
// echo (new Cleaner())->clean($string);
