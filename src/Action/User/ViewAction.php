<?php
declare(strict_types=1);

namespace App\Action\User;

use App\Action\AbstractAction as Action;
use App\Service\User\UserService;
use App\Responder\HtmlResponder;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Odan\Session\FlashInterface as Flash;
use Slim\Interfaces\RouteParserInterface as RouteParser;

class ViewAction extends Action
{
    private HtmlResponder $responder;

    private UserService $userService;

    private Flash $flash;

    private RouteParser $router;

    public function __construct(
        HtmlResponder $responder,
        UserService $userService,
        Flash $flash,
        RouteParser $router
    ) {
        $this->responder = $responder;
        $this->userService = $userService;
        $this->flash = $flash;
        $this->router = $router;
    }

    public function __invoke(Request $request, Response $response, string $id): Response
    {
        $user = $this->userService->fetch($id);
        if (!$user) {
            $this->flash->add('error', 'User not found');
            return $this->responder->redirect($response, $this->router->urlFor('users.index'));
        }

        return $this->responder->render($response, 'users/view', ['user' => $user->getData()]);
    }
}
