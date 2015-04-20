<?php namespace Phasty\Common\Traits\Sluggable;


interface SluggableInterface {

	public function getSlug();

	public function sluggify($force=false);

	public function resluggify();

}