# Cacofony
Custom Application Code Overlay for Symfony framework

*** **Consolidation completed for Symfony 5.4 but may missed something** ***

## Controller
* Parent controller 'AppController' is provided
* AppController can automatically dump template parameters on dev:
when modifying template, you can see every available parameters
* AppController can automatically retrieve template
(in "{controllerName}/{functionName}.html.twig") by using ``return renderTemplate([])``
* AppController can redirect with cookies already defined.
Methods are added to redirect to referer or to self route.

Controller use a specific Request object (extending standard one),
and should be declared in public/index file:
```
//use Symfony\Component\HttpFoundation\Request
use Keiwen\Cacofony\Http\Request
```
Declare its use in kernel if needed
```
$kernel = new Kernel($_SERVER['APP_ENV'], (bool) $_SERVER['APP_DEBUG']);
$request = Request::createFromGlobals();
$response = $kernel->handle($request);
$response->send();
$kernel->terminate($request, $response);
```

## Restrict to Role
This annotation could be used to check user role.
Could be on a single action or for a whole controller.
```
/**
 * @RestrictToRole("admin")
 */
class TestController extends DefaultController
```
In this example, system will check if user has 'ROLE_ADMIN' role.
If not, an AccessDeniedException is raised.
Value in annotation don't need to be uppercase or have the 'role_' prefix.

## Template Param
This attribute could be used for 'constants' given to templates.
It could be defined on a single action, or for the full controller
```
 #[TemplateParameter(name: "section", value: "test")
class TestController extends DefaultController
```
In this example, all actions in this controller will automatically return
a 'section' parameter, with value 'test'

## Translation
For translations purpose, you can set a specific locale
(default 'transCode') to display translations code instead of
actual  translated strings. Useful when working on 
translations from running application.

## Twig
### Filters
* ``label`` add ':' at the end of your text.
Depending on locale, it can add non-breakable space (as in french)
* ``str_limit`` to limit displayed string to given length, ending with '...' by default
* ``ucfirst`` switch first letter to uppercase
### Methods
* ``getRoute()`` returns route name
* ``hasRole('user')`` check if current user has specified role.
Value don't need to be uppercase or have the 'role_' prefix.

## EntityRegistry
Can save, remove, detach or copy an entity or a list of entities.
Each methods include an optional commit parameter (default true).
In controller:
```
$this->getEntityRegistry()->saveObject($entity);
```

