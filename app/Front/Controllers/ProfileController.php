<?php namespace Phasty\Front\Controllers;

use Phasty\Common\Models\PasswordChanges;
use Phasty\Common\Models\UserProfile as Profile;
use Phasty\Common\Repo\UserProfile\PhalconUserProfile;
use Phasty\Front\Forms\ChangePasswordForm;
use Phasty\Front\Forms\ProfileEditForm;

class ProfileController extends ControllerBase
{
    protected $pspt;
    protected $profileEditForm;
    protected $changePasswordForm;
    protected $repo;
    protected $profile;

    protected function initialize()
    {
        $this->repo = new PhalconUserProfile();
        $this->profileEditForm = new ProfileEditForm();
        $this->changePasswordForm = new ChangePasswordForm();
        $this->pspt = $this->session->get('auth');
        $this->profile = $this->repo->byUserId($this->pspt['id']);
        parent::initialize();
    }

    public function indexAction()
    {
        $this->tag->setTitle("Customer data");
        $this->view->profile = $this->profile;
        $this->view->breadcrumbs = array('General information');
    }

    public function editAction()
    {
        if (!$this->request->isPost()) {
            $this->tag->setDefault('name', $this->profile->user->name);
            $this->tag->setDefault('email', $this->profile->user->email);
            $this->tag->setDefault('address', $this->profile->address);
            $this->tag->setDefault('phone', $this->profile->phone);
        } elseif ($this->request->isPost() && $this->profileEditForm->isValid($this->request->getPost())) {
            $this->profile->update($this->request->getPost());
        }
        $this->view->form = $this->profileEditForm;
        $this->view->breadcrumbs = ['Общая информация' => '/profile/index', 'Редактировать'];
    }

    /**
     * Users must use this action to change its password
     *
     */
    public function changePasswordAction()
    {
        if ($this->request->isPost() && $this->changePasswordForm->isValid($this->request->getPost())) {
            $this->profile->user->password = $this->security->hash($this->request->getPost('password'));
            $this->profile->user->mustChangePassword = 'N';

            $passwordChange = new PasswordChanges();
            $passwordChange->usersId = $this->profile->user_id;
            $passwordChange->ipAddress = $this->request->getClientAddress();
            $passwordChange->userAgent = $this->request->getUserAgent();

            $this->flashSession->success('Password was successfully changed.');

            $this->tag->resetInput();
            return $this->response->redirect("profile/index");

        }
        $this->view->form = $this->changePasswordForm;
        $this->view->breadcrumbs = array('Общая информация' => '/profile/index', 'Сменить пароль');

    }
}