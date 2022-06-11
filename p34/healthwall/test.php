<?php
$post='Rm9yIHdlaWdodCBsb3NzIGVuc3VyZSByZWd1bGFyIHBoeXNpY2FsIGFjdGl2aXR5IGxpa2Ugd2Fsa2luZywgam9nZ2luZyAsIHJ1bm5pbmcgIG9yIGV2ZW4gZ29pbmcgdG8gZ3ltLg0KSW4geW91ciBkaWV0IHlvdSBjYW4gaW5jbHVkZSBtb3JlIG9mIA0KLSBQcm90ZWluIGxpa2UgbG93IGZhdCBtaWxrLCBjb3R0YWdlIGNoZWVzZSwg';
$post = preg_replace('~[\r\n]+~', '', $post);
    if(base64_encode(base64_decode($post)) === $post){
    $post=base64_decode($post);
}
echo $post;

?>