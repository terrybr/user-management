<?php
declare(strict_types=1);

namespace App\Controller;

use App\Domain\User\Service\UserAuthService;
use App\Domain\User\Service\UserSessionService;
use Odan\Session\FlashInterface as Flash;
use Psr\Http\Message\ResponseInterface;
use Slim\Interfaces\RouteParserInterface as RouteParser;
use Slim\Http\Response;
use Slim\Http\ServerRequest as Request;
use Slim\Views\Twig;

class AuthController extends Controller
{
    private UserAuthService $userAuthService;

    private RouteParser $router;

    private Twig $view;

    private Flash $flash;

    public function __construct(
        UserAuthService $userAuthService,
        UserSessionService $userSessionService,
        RouteParser $router,
        Twig $view,
        Flash $flash
    ) {
        $this->userAuthService = $userAuthService;
        $this->userSessionService = $userSessionService;
        $this->router = $router;
        $this->view = $view;
        $this->flash = $flash;
    }

    public function login(Request $request, Response $response): ResponseInterface
    {
        $username = $request->getParsedBodyParam('username', '');
        $password = $request->getParsedBodyParam('password', '');

        if ($request->isPost()) {
            $user = $this->userAuthService->authenticate($username, $password);
            if ($user) {
                $this->userAuthService->updateLastLogin($user->id);
                $this->userSessionService->set($user);

                $this->flash->add('auth', sprintf(
                    'Welcome, %s! Last login: %s',
                    $user->name,
                    $user->lastLogin ? $user->lastLogin->format('l, j. F Y H:i') : 'never'
                ));
                return $response->withRedirect($this->router->urlFor('users.index'));
            } else {
                $this->flash->add('auth', 'Invalid username or password');
            }
        }

        return $this->view->render($response, 'Auth/login.twig', [
            'username' => $username
        ]);
    }

    public function logout(Request $request, Response $response): ResponseInterface
    {
        $this->userSessionService->clear();
        $this->flash->add('default', 'You have been logged out');

        return $response->withRedirect($this->router->urlFor('auth.login'));
    }
}
