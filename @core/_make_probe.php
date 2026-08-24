<?php
/* Build a standalone probe page from the REAL served page pieces, then we run it in headless Edge.
   Any JS error is captured into <div id=jserr>. Delete after debugging. */

$served = file_get_contents(sys_get_temp_dir() . '\\campaign-page.html');

/* 1. extract the inline media uploader script (the one containing Dropzone.options) */
if (!preg_match('/<script>\s*\(function \(\$\) \{[\s\S]*?Dropzone\.options[\s\S]*?\}\)\(jQuery\);\s*<\/script>/', $served, $m)) {
    exit("could not locate media inline script\n");
}
$mediaJs = $m[0];

/* 2. extract the modal block */
$i = strpos($served, 'id="media_upload_modal"');
$i = strrpos(substr($served, 0, $i), '<div');           /* back up to the modal's opening tag */
$depth = 0; $end = $i;
for ($k = $i; $k < $i + 40000 && $k < strlen($served); $k++) {
    if ($served[$k] === '<' && substr($served, $k, 4) === '<div') $depth++;
    elseif ($served[$k] === '<' && substr($served, $k, 6) === '</div>') { $depth--; if ($depth === 0) { $end = $k + 6; break; } }
}
$modal = ($end > $i) ? substr($served, $i, $end - $i) : '';

$probe = '<!DOCTYPE html><html><head><meta charset="utf-8">'
    . '<link rel="stylesheet" href="/assets/frontend/css/bootstrap.min.css">'
    . '<link rel="stylesheet" href="/assets/backend/css/dropzone.css">'
    . '<link rel="stylesheet" href="/assets/backend/css/media-uploader.css">'
    . '<script src="/assets/frontend/js/jquery-3.4.1.min.js"></script>'
    . '</head><body>'
    . '<div id="jserr" style="background:#fee;color:#900;padding:8px;font-family:monospace"></div>'
    . '<script>window.onerror=function(msg,src,line,col){document.getElementById("jserr").innerHTML+="ERROR: "+msg+" @ "+line+":"+col+"<br>";};</script>'
    . '<button type="button" class="media_upload_form_btn" data-toggle="modal" data-target="#media_upload_modal">Choose</button>'
    . $modal
    . '<script src="/assets/frontend/js/bootstrap.bundle.min.js"></script>'
    . '<script src="/assets/backend/js/dropzone.js"></script>'
    . $mediaJs
    . '<script>window.addEventListener("load",function(){var d=document.getElementById("placeholderfForm");'
    . 'var ok=d&&(d.classList.contains("dz-clickable")||d.querySelector(".dz-message"));'
    . 'document.getElementById("jserr").innerHTML+=(ok?"DROPZONE INITIALIZED OK":"DROPZONE NOT INITIALIZED");});</script>'
    . '</body></html>';

file_put_contents('public/_media_probe.html', $probe);
echo "probe written, mediaJs len=" . strlen($mediaJs) . ", modal len=" . strlen($modal) . "\n";
