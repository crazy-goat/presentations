<?php

namespace app\controller;

use support\Request;

class IndexController
{
    public function index(Request $request)
    {
        return response(<<<'HTML'
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Running PHP Apps FROM scratch</title>
<style>
  body {
    font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
    background: #1a1a2e;
    color: #eee;
    display: flex;
    align-items: center;
    justify-content: center;
    min-height: 100vh;
    margin: 0;
  }
  .card {
    text-align: center;
    max-width: 600px;
    padding: 3rem 2rem;
  }
  h1 {
    font-size: 2.5rem;
    font-weight: 700;
    margin-bottom: 1rem;
    line-height: 1.2;
  }
  h1 code {
    color: #7ec8e3;
    background: rgba(126,200,227,.15);
    padding: .1em .3em;
    border-radius: 4px;
  }
  p {
    color: #aaa;
    font-size: 1.1rem;
    margin-bottom: 2.5rem;
    line-height: 1.6;
  }
  a.btn {
    display: inline-block;
    background: #7ec8e3;
    color: #1a1a2e;
    font-weight: 700;
    font-size: 1.1rem;
    padding: .9rem 2.5rem;
    border-radius: 6px;
    text-decoration: none;
    transition: opacity .15s;
  }
  a.btn:hover { opacity: .85; }
  .meta {
    margin-top: 2rem;
    font-size: .85rem;
    color: #555;
  }
</style>
</head>
<body>
<div class="card">
  <h1>Running PHP Apps <code>FROM scratch</code></h1>
  <p>An epic journey through 20 years of PHP deployment — from FTP to a statically compiled binary in a scratch image the size of a decent espresso.</p>
  <a class="btn" href="/slides/">Start presentation</a>
  <div class="meta">Powered by Workerman &amp; PHP micro.sfx</div>
</div>
</body>
</html>
HTML, 200, ['Content-Type' => 'text/html; charset=utf-8']);
    }
}
