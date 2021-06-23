# Cacofony
Custom Application Code Overlay for Symfony framework

*** **Consolidation completed for Symfony 4.3 but may missed something** ***

## Controller
* Parent controller adds cache management methods
* AppController can redirect with cookies already defined
* AppController can automatically dump template parameters on dev
(for action with @template annotation)
* ApiController format resulting json
    * ref_url: reference to url called
    * parameters: list of parameters received
    * result: answer from API
    * http_code: HTTP code response
    * message: specific message returned by the API

Controller use a specific Request object (extending standard one),
and should be declared in public/index file:
```
//use Symfony\Component\HttpFoundation\Request
use Keiwen\Cacofony\Http\Request
```

## ParamFetcher
Controller method could be annotated with ParamFetcher annotations
to declare request (get and/or post) parameters for the route. 
Parameters can be required, have some requirements (error if not met)
or filter (invalid chars removed without error triggered).   
ParamFetcher is filled if declared as controller parameters, and sends
a 400 HTTP response when error occurs.  
Cacofony includes integration for NelmioApiDocBundle with these annotations
```
/**
 * Action executed if GET request with apiKey parameter.
 * variable '$apiKey' in action will be an alphanumerical string.
 *
 * @Get("/route", name="routeName")
 * @GetParam(name="apiKey", description="required key for API access", required=true, filter="scalar")
 */
public function action(ParamFetcher $paramFetcher) {
    $apiKey = $paramFetcher->get('apiKey');
}
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
This annotation could be used for 'constants' given to templates.
It could be defined on a single action, or for the full controller
```
/**
 * @TemplateParameter("section", paramValue="test")
 */
class TestController extends DefaultController
```
In this example, all actions in this controller will automatically return
a 'section' parameter, with value 'test'

## TransCode
For translations purpose, you can set a specific locale
(default 'transCode') to display translations code instead of
actual  translated strings. Useful when working on 
translations from running application.

## EntityRegistry
Can save, remove, detach or copy an entity or a list of entities.
Each methods include an optional commit parameter (default true).
In controller:
```
$this->getEntityRegistry()->saveObject($entity);
```

