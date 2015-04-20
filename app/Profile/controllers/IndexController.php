<?php
namespace Shop\Profile\Controllers;

use Shop\Models\UserProfile as Profile;

class IndexController extends ControllerBase
{

    protected function initialize()
    {
        $this->view->setTemplateAfter('main');
        parent::initialize();
    }

    public function indexAction()
    {
        $this->tag->setTitle("Данные профиля");
        $profile = Profile::findFirst(array("conditions" => "user_id = ?1", "bind" => array(1 => $this->identity['id'])));
        if (!$profile) {
            return $this->response->redirect('session/login');
        }
        $this->view->setVar('profile', $profile);
        $this->view->auth = $this->identity;
        $this->view->breadcrumbs = array('Общая информация');
    }

    public function editAction()
    {
        //Query the active user
        $user = $this->auth->getUser();
        if (!$user) {
            return $this->response->redirect('session/login');
        }

        $request = $this->request;
        $form = new \Shop\Forms\ProfileForm();
        $profile = \Shop\Models\UserProfile::findFirst("user_id = '$user->id'");

        if (!$request->isPost()) {
            $this->tag->setDefault('username', $user->name);
            $this->tag->setDefault('email', $user->email);
            $this->tag->setDefault('address', $profile->address);
            $this->tag->setDefault('phone', $profile->phone);
        } elseif ($request->isPost() && ($form->isValid($this->request->getPost()) != false)) {
            $user->profile->user_id = $user->id;
            $user->profile->address = $request->getPost('address', 'striptags');
            $user->profile->phone = $request->getPost('phone', 'striptags');
            $user->name = $request->getPost('username', 'striptags');
            $user->email = $request->getPost('email', 'email');

            if ($user->save()) {
                $this->flash->success('Информация в профиле обновлена');
                return $this->response->redirect("profile/index");
            } else {
                $this->flash->error($user->getMessages());
            }
        }
        $this->view->form = $form;
        $this->view->auth = $this->identity;
        $this->view->breadcrumbs = array('Общая информация' => '/profile/index', 'Редактировать');
    }

    /**
     * Users must use this action to change its password
     *
     */
    public function changePasswordAction()
    {
        $form = new \Shop\Forms\ChangePasswordForm();

        if ($this->request->isPost()) {

            if (!$form->isValid($this->request->getPost())) {

                foreach ($form->getMessages() as $message) {
                    $this->flash->error($message);
                }

            } else {

                $user = $this->auth->getUser();

                $user->password = $this->security->hash($this->request->getPost('password'));
                $user->mustChangePassword = 'N';

                $passwordChange = new \Shop\Models\PasswordChanges();
                $passwordChange->user = $user;
                $passwordChange->ipAddress = $this->request->getClientAddress();
                $passwordChange->userAgent = $this->request->getUserAgent();

                if (!$passwordChange->save()) {
                    $this->flash->error($passwordChange->getMessages());
                } else {

                    $this->flashSession->success('Пароль был успешно изменен.');

                    $this->tag->resetInput();
                    return $this->response->redirect("profile/index");
                }

            }

        }

        $this->view->form = $form;
        $this->view->auth = $this->identity;
        $this->view->breadcrumbs = array('Общая информация' => '/profile/index', 'Сменить пароль');
    }
}
