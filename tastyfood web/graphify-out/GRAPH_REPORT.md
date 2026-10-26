# Graph Report - TastyFood  (2026-10-09)

## Corpus Check
- cluster-only mode — file stats not available

## Summary
- 558 nodes · 881 edges · 88 communities (20 shown, 68 thin omitted)
- Extraction: 100% EXTRACTED · 0% INFERRED · 0% AMBIGUOUS
- Token cost: 0 input · 0 output

## Graph Freshness
- Built from commit: `cf32f635`
- Run `git rev-parse HEAD` and compare to check if the graph is stale.
- Run `graphify update .` after code changes (no API cost).

## Community Hubs (Navigation)
- Conversation
- Illuminate\Http\Request
- Illuminate\Database\Eloquent\Model
- Illuminate\Database\Migrations\Migration
- Order
- package.json
- Gallery
- PaymentMethod
- Menu
- Payment
- composer.json
- require-dev
- scripts
- config
- laravel-boost
- psr-4
- require
- logging.php
- app.blade.php
- ExampleTest
- artisan
- autoload-dev
- extra
- laravel-boost
- console.php
- laravel-boost
- laravel-boost

## God Nodes (most connected - your core abstractions)
1. `Controller` - 40 edges
2. `Conversation` - 30 edges
3. `Order` - 29 edges
4. `Message` - 28 edges
5. `Menu` - 25 edges
6. `Payment` - 20 edges
7. `News` - 19 edges
8. `MessageSent` - 18 edges
9. `Gallery` - 17 edges
10. `Setting` - 15 edges

## Surprising Connections (you probably didn't know these)
- `AdminFlowTest` --references--> `Admin`  [EXTRACTED]
  tests/Feature/AdminFlowTest.php → app/Models/Admin.php
- `NewOrderReceived` --references--> `Order`  [EXTRACTED]
  app/Events/NewOrderReceived.php → app/Models/Order.php
- `ChatController` --inherits--> `Controller`  [EXTRACTED]
  app/Http/Controllers/Admin/ChatController.php → app/Http/Controllers/Controller.php
- `ChatController` --inherits--> `Controller`  [EXTRACTED]
  app/Http/Controllers/ChatController.php → app/Http/Controllers/Controller.php
- `GalleryController` --inherits--> `Controller`  [EXTRACTED]
  app/Http/Controllers/Admin/GalleryController.php → app/Http/Controllers/Controller.php

## Import Cycles
- None detected.

## Communities (88 total, 68 thin omitted)

### Community 0 - "Conversation"
Cohesion: 0.06
Nodes (19): MessageRead, MessageSent, NewOrderReceived, UserTyping, ChatController, ChatController, SendOrderSystemMessage, Conversation (+11 more)

### Community 1 - "Illuminate\Http\Request"
Cohesion: 0.05
Nodes (23): AboutController, AuthController, DashboardController, MessageController, OrderManagementController, SettingController, LoginController, RegisterController (+15 more)

### Community 2 - "Illuminate\Database\Eloquent\Model"
Cohesion: 0.05
Nodes (20): NewsController, NewsController, Admin, News, OrderItem, Setting, User, UserFactory (+12 more)

### Community 3 - "Illuminate\Database\Migrations\Migration"
Cohesion: 0.08
Nodes (3): Illuminate\Database\Migrations\Migration, Illuminate\Database\Schema\Blueprint, Illuminate\Support\Facades\Schema

### Community 4 - "Order"
Cohesion: 0.06
Nodes (8): OrderController, OrderController, Order, AppServiceProvider, DeliveryService, Illuminate\Support\Facades\DB, Illuminate\Support\Facades\Http, Illuminate\Support\ServiceProvider

### Community 5 - "package.json"
Cohesion: 0.06
Nodes (30): dependencies, laravel-echo, leaflet, pusher-js, devDependencies, concurrently, laravel-vite-plugin, tailwindcss (+22 more)

### Community 6 - "Gallery"
Cohesion: 0.09
Nodes (10): GalleryController, GalleryController, HomeController, Gallery, Illuminate\Foundation\Testing\RefreshDatabase, Illuminate\Foundation\Testing\TestCase, Illuminate\Http\UploadedFile, AdminFlowTest (+2 more)

### Community 7 - "PaymentMethod"
Cohesion: 0.13
Nodes (5): PaymentMethodController, PaymentController, PaymentMethod, Illuminate\Database\Eloquent\SoftDeletes, Illuminate\Support\Facades\Storage

### Community 8 - "Menu"
Cohesion: 0.13
Nodes (3): MenuController, MenuController, Menu

### Community 10 - "composer.json"
Cohesion: 0.22
Nodes (8): description, keywords, license, minimum-stability, name, prefer-stable, $schema, type

### Community 11 - "require-dev"
Cohesion: 0.22
Nodes (9): require-dev, fakerphp/faker, laravel/boost, laravel/pail, laravel/pao, laravel/pint, mockery/mockery, nunomaduro/collision (+1 more)

### Community 12 - "scripts"
Cohesion: 0.22
Nodes (9): scripts, dev, post-autoload-dump, post-create-project-cmd, post-root-package-install, post-update-cmd, pre-package-uninstall, setup (+1 more)

### Community 13 - "config"
Cohesion: 0.29
Nodes (7): pestphp/pest-plugin, php-http/discovery, config, allow-plugins, optimize-autoloader, preferred-install, sort-packages

### Community 14 - "laravel-boost"
Cohesion: 0.29
Nodes (6): command, enabled, type, mcp, laravel-boost, $schema

### Community 15 - "psr-4"
Cohesion: 0.40
Nodes (5): autoload, psr-4, App\\, Database\\Factories\\, Database\\Seeders\\

### Community 16 - "require"
Cohesion: 0.40
Nodes (5): require, laravel/framework, laravel/reverb, laravel/tinker, php

### Community 17 - "logging.php"
Cohesion: 0.40
Nodes (4): Monolog\Handler\NullHandler, Monolog\Handler\StreamHandler, Monolog\Handler\SyslogUdpHandler, Monolog\Processor\PsrLogMessageProcessor

### Community 18 - "app.blade.php"
Cohesion: 0.50
Nodes (3): components.chat-widget, components.footer, components.navbar

### Community 21 - "autoload-dev"
Cohesion: 0.67
Nodes (3): autoload-dev, psr-4, Tests\\

### Community 22 - "extra"
Cohesion: 0.67
Nodes (3): extra, laravel, dont-discover

## Knowledge Gaps
- **65 isolated node(s):** `description`, `keywords`, `license`, `minimum-stability`, `name` (+60 more)
  These have ≤1 connection - possible missing edges or undocumented components. (Counts symbols only; 267 node(s) total have ≤1 connection when file, concept and rationale nodes are included.)
- **68 thin communities (<3 nodes) omitted from report** — run `graphify query` to explore isolated nodes.

## Suggested Questions
_Questions this graph is uniquely positioned to answer:_

- **Why does `Controller` connect `Illuminate\Http\Request` to `Conversation`, `Illuminate\Database\Eloquent\Model`, `Order`, `Gallery`, `PaymentMethod`, `Menu`, `Payment`?**
  _High betweenness centrality (0.066) - this node is a cross-community bridge._
- **Why does `Order` connect `Order` to `Conversation`, `Illuminate\Http\Request`, `Illuminate\Database\Eloquent\Model`?**
  _High betweenness centrality (0.052) - this node is a cross-community bridge._
- **Why does `Message` connect `Conversation` to `Illuminate\Database\Eloquent\Model`, `Gallery`?**
  _High betweenness centrality (0.038) - this node is a cross-community bridge._
- **What connects `description`, `keywords`, `license` to the rest of the system?**
  _65 weakly-connected nodes found - possible documentation gaps or missing edges._
- **Should `Conversation` be split into smaller, more focused modules?**
  _Cohesion score 0.06025039123630673 - nodes in this community are weakly interconnected._
- **Should `Illuminate\Http\Request` be split into smaller, more focused modules?**
  _Cohesion score 0.05046948356807512 - nodes in this community are weakly interconnected._
- **Should `Illuminate\Database\Eloquent\Model` be split into smaller, more focused modules?**
  _Cohesion score 0.05182443151771549 - nodes in this community are weakly interconnected._