<?php
class PagesController{
	public static function index($uri){
		return view('pages.index', ['var' => 'some var']);
	}
}