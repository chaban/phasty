<?php namespace Phasty\Common\Repo\Pages;

abstract class AbstractArticleDecorator implements PagesInterface {

	protected $nextPage;

	public function __construct(PagesInterface $nextPage) {
		$this->nextPage = $nextPage;
	}

	/**
	 * {@inheritdoc}
	 */
	public function byId($id) {
		return $this->nextPage->byId($id);
	}

	/**
	 * {@inheritdoc}
	 */
	public function byPage($page = 1, $limit = 10, $all = false) {
		return $this->nextPage->byPage($page, $limit, $all);
	}

	/**
	 * {@inheritdoc}
	 */
	public function bySlug($slug) {
		return $this->nextPage->bySlug($slug);
	}

	/**
	 * {@inheritdoc}
	 */
	public function byTag($tag, $page = 1, $limit = 10) {
		return $this->nextPage->byTag($tag, $page, $limit);
	}

	/**
	 * {@inheritdoc}
	 */
	public function create(array $data) {
		return $this->nextPage->create($data);
	}

	/**
	 * {@inheritdoc}
	 */
	public function update(array $data) {
		return $this->nextPage->update($data);
	}

}
