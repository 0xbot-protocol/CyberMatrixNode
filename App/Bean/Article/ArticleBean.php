<?php


namespace App\Bean\Article;


use App\Bean\BaseBean;
use EasySwoole\Spl\SplBean;

class ArticleBean extends SplBean
{
    public $id;
    public  $app; //app

    public  $title;
    public  $cid;
    public  $summary;
    public  $content;
    public  $show; //是否显示
    public  $hot; //热门
    public  $top; //置顶

    public  $up; //赞
    public  $down; //踩
    public  $pic_url;
    public  $deleted; //踩



}