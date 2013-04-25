phaxsi
======

What is Phaxsi PHP Framework?
=============================

Phaxsi is a modern PHP framework built from the ground up with simplicity and code reuse in mind. While it is a standard Model-View-Controller framework, its solution to many web development problems is new and fresh. 

Main Features
=============
  
**Model-View-Controller**
MVC architecture forces separation of logic, presentation and data. 

*SQL Query Builder*
With a flexible syntax reminiscent of LINQ, you will be able to securely query the database without writing any SQL.

*Highly Extensible*

Points of extensibility include plugins, custom form components, custom helpers, custom views and, of course, modules (see Design Goals).

*Validation*

By adding a few rules to your form inputs, you will get validation on the server side as well as on the client side. 

*Built-in Ajax Support*

Automatically update page fragments with very few instructions or generate JSON responses from your controller. 

*Output Caching*

Cache whole pages or page fragments (blocks) with 1 line of code using the file system, memcache or a sqlite database.

*I18n*

Easily create fully translated webpages by just adding translation files and language-sensitive resources.

*Error Handling Module*

A powerful module that lets you handle and trace any application error during development, while showing a friendly page to your users in production.

*Tracing and Profiling Plugin*

This plugin makes the application flow visible to the developer. It reports the exection time of your code, including database queries.  

*Authorization Plugin*

Without leaving the PHP environment, secure your controllers with access control. 

*Authentication Module*

Complete login and registration system included with this module.

*Page Blocks*

With blocks, it is possible to create isolated widgets that can be used accross different pages.

*Layouts*

Layouts are those parts of the page that don't change much, like, for example, the header and the footer. You can put this code in just one part and Orange will bring the pieces together.

*Shell Scripting*

Orange supports shell scripting out of the box, providing its organization and power to shell scripts.

Primary Design Goals
====================

*Simplicity*

Ease of use was the most important design goal. With Phaxsi, most tasks are achieved in simple ways, configuration is near zero and the learning curve is in the worst case equivalent to other mainstream frameworks. 

*Code Reuse*

Phaxsi was born while creating reusable components for community-driven websites which were very different to one another. The goal was to enable the reuse of whole systems like, for instance, a comment system or an authentication system. 

To this end, the framework is organized in modules where the code of each one can be completely isolated from the others. In some cases, reusing a whole application system in a new application, can be as simple as copying and pasting the module's folder.

*High Performance*

The framework architecture is extremely lightweight. Loosely coupled components are put together to make this possible. As a result, Orange features an ideal performance for intermediate to big websites.

