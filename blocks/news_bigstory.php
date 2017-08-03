<?php
defined('XOOPS_ROOT_PATH') || die('XOOPS root path not defined');
/**
 * Solves issue when upgrading xoops version
 * Paths not set and block would not work
 */
if (!defined('XNEWS_MODULE_PATH')) {
    define("XNEWS_SUBPREFIX", "nw");
    define("XNEWS_MODULE_DIRNAME", "xnews");
    define("XNEWS_MODULE_PATH", XOOPS_ROOT_PATH . "/modules/" . XNEWS_MODULE_DIRNAME);
    define("XNEWS_MODULE_URL", XOOPS_URL . "/modules/" . XNEWS_MODULE_DIRNAME);
    define("XNEWS_UPLOADS_NEWS_PATH", XOOPS_ROOT_PATH . "/uploads/" . XNEWS_MODULE_DIRNAME);
    define("XNEWS_TOPICS_FILES_PATH", XOOPS_ROOT_PATH . "/uploads/" . XNEWS_MODULE_DIRNAME . "/topics");
    define("XNEWS_ATTACHED_FILES_PATH", XOOPS_ROOT_PATH . "/uploads/" . XNEWS_MODULE_DIRNAME . "/attached");
    define("XNEWS_TOPICS_FILES_URL", XOOPS_URL . "/uploads/" . XNEWS_MODULE_DIRNAME . "/topics");
    define("XNEWS_ATTACHED_FILES_URL", XOOPS_URL . "/uploads/" . XNEWS_MODULE_DIRNAME . "/attached");
}

function nw_b_news_bigstory_show()
{
    require_once XNEWS_MODULE_PATH . '/include/functions.php';
    require_once XNEWS_MODULE_PATH . '/class/class.newsstory.php';
    $myts       = MyTextSanitizer::getInstance();
    $restricted = $xnews->getConfig('restrictindex');
    $dateformat = $xnews->getConfig('dateformat');
    $infotips   = $xnews->getConfig('infotips');

    $block    = array();
    $onestory = new nw_NewsStory();
    $stories  = $onestory->getBigStory(1, 0, $restricted, 0, 1, true, 'counter');
    if (count($stories) == 0) {
        $block['message'] = _MB_NW_NOTYET;
    } else {
        foreach ($stories as $key => $story) {
            $htmltitle = '';
            if ($infotips > 0) {
                $block['infotips'] = nw_make_infotips($story->hometext());
                $htmltitle         = ' title="' . $block['infotips'] . '"';
            }
            //DNPROSSI ADDED
            $block['newsmodule_url']    = XNEWS_MODULE_URL;
            $block['htmltitle']         = $htmltitle;
            $block['message']           = _MB_NW_TMRSI;
            $block['story_title']       = $story->title('Show');
            $block['story_id']          = $story->storyid();
            $block['story_date']        = formatTimestamp($story->published(), $dateformat);
            $block['story_hits']        = $story->counter();
            $block['story_rating']      = $story->rating();
            $block['story_votes']       = $story->votes();
            $block['story_author']      = $story->uname();
            $block['story_text']        = $story->hometext();
            $block['story_topic_title'] = $story->topic_title();
            $block['story_topic_color'] = '#' . $myts->displayTarea($story->topic_color);
        }
    }
    // DNPROSSI SEO
    $seo_enabled = $xnews->getConfig('seo_enable');
    if ($seo_enabled != 0) {
        $block['urlrewrite'] = "true";
    } else {
        $block['urlrewrite'] = "false";
    }

    return $block;
}

function nw_b_news_bigstory_onthefly($options)
{
    $options = explode('|', $options);
    $block   =& nw_b_news_bigstory_show($options);

    $tpl = new XoopsTpl();
    $tpl->assign('block', $block);
    $tpl->display('db:nw_news_block_bigstory.html');
}