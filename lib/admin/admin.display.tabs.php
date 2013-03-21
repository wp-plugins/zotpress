<?php

    if (isset($_GET['accounts']) && $_GET['accounts'] == "true")
        $tagpage = "accounts";
    else if (isset($_GET['options']) && $_GET['options'] == "true")
        $tagpage = "options";
    else if (isset($_GET['help']) && $_GET['help'] == "true")
        $tagpage = "help";
    else
        $tagpage = "default";

?>

<div id="zp-Zotpress-Navigation">

    <div id="zp-Icon" title="Zotero + WordPress = Zotpress"><br /></div>

    <div class="nav">
        <a class="nav-item <?php if ($tagpage == "default") echo "nav-tab-active"; ?>" href="admin.php?page=Zotpress">Browse</a>
        <a class="nav-item <?php if ($tagpage == "accounts") echo "nav-tab-active"; ?>" href="admin.php?page=Zotpress&amp;accounts=true">Accounts</a>
        <a class="nav-item <?php if ($tagpage == "options") echo "nav-tab-active"; ?>" href="admin.php?page=Zotpress&amp;options=true">Options</a>
        <a class="nav-item <?php if ($tagpage == "help") echo "nav-tab-active"; ?>" href="admin.php?page=Zotpress&amp;help=true">Help</a>
    </div>

</div><!-- #zp-Zotpress-Navigation -->