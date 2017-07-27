<?php
/*
 * Created on 03/12/2008
 *
 * This page will display a list of articles which belong to a tag
 *
 * @package News
 * @author Hervé Thouzard of Instant Zero
 * @copyright (c) Instant Zero - http://www.instant-zero.com
 */
require_once __DIR__ . '/header.php';

if (!$xnews->getConfig('tags')) {
    redirect_header('index.php', 3, _ERRORS);
}
require_once XOOPS_ROOT_PATH . '/modules/tag/list.tag.php';
