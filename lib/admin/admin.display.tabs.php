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

<div id="icon-themes" class="icon32"><br /></div>

<h2 class="nav-tab-wrapper">
    <a class="nav-tab <?php if ($tagpage == "default") echo "nav-tab-active"; ?>" href="admin.php?page=Zotpress">Browse</a>
    <a class="nav-tab <?php if ($tagpage == "accounts") echo "nav-tab-active"; ?>" href="admin.php?page=Zotpress&amp;accounts=true">Accounts</a>
    <a class="nav-tab <?php if ($tagpage == "options") echo "nav-tab-active"; ?>" href="admin.php?page=Zotpress&amp;options=true">Options</a>
    <a class="nav-tab <?php if ($tagpage == "help") echo "nav-tab-active"; ?>" href="admin.php?page=Zotpress&amp;help=true">Help</a>
</h2>
