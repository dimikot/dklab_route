Dklab_Route: a library to match() and assemble() URLs using various methods
(C) Dmitry Koterov, http://en.dklab.ru/lib/Dklab_Route/

This library parses and builds URLs according to specified rules. It is
a bit low-level, but powerful.


Feature: match() and assemble() URLs using named parameters
-----------------------------------------------------------

[post_edit]
url = /post/id=(\d+)/edit
ctrl = Post_Edit

[post_add]
url = /post/add
ctrl = Post_Add

$router->assemble(array("name" => "post_edit", "id" => 10));
$matched = $router->match("http://example.com/post/123/edit");


Feature: deal with sub-domains
------------------------------

[post_edit]
url = "username=(.*)/post/id=(\d+)/edit"
ctrl = Post_Edit

[post_add]
url = "username=(.*)/post/add"
ctrl = Post_Add

$router->assemble(array("name" => "post_edit", "username" => "ivan", "id" => 10));
$matched = $router->match("http://ivan.example.com/post/123/edit");


Feature: deal with domain zones
-------------------------------

Suppose each developer has its own domain zone:

example.com.IVAN.dev.local
example.com.PETR.dev.local
example.com.ALEX.dev.local

The library supports transparent "*.dev.local" (or similar) zones addition
or subtraction for each URL it is used for. You may specify "*.dev.local"
rule, and the developer name (e.g. IVAN) will be deduced automatically
based on the current HTTP_HOST.

E.g. $router->assemble(array("name" => "post_edit", "username" => "ivan", "id" => 10));
may be translated to "http://ivan.example.com.PETR.dev.local/post/123/edit".
