<?php


namespace App\Bean\Article;


use App\Bean\BaseBean;
use EasySwoole\Spl\SplBean;

class ArticleCategoryBean extends SplBean
{

    public  $id;//

    public  $app; //app
    public  $pid; //父分类
    public  $title;
    public  $sort; //排序位置
    public  $intro;
    public  $icon; //图标
    public  $slide; //轮播图

    public  $show; //是否显示

}