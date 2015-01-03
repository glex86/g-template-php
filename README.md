#G-Template Engine
=================
Ultrafast and light compiling template engine for PHP5 + Zend OpCache

The sources are based on TemplateLite (http://templatelite.sourceforge.net/) and Smarty (http://www.smarty.net/)

Basically G-Template Engine wants to be a bare minimal smarty alternative.
This is the first public techpreview so use it on your own risk!

##Requirements
- PHP 5.5
- Zend OpCache 7.0.4-dev

##Main concepts
In the first words i need to say sorry for my bad englis :(
I think Smarty is the best template engine for PHP but sometimes it's too slow or eats a lot of memory.
Some years ago I found the TemplateLite engine. It is much faster and it doesn't need a lot of memory.
But TemplateLite is very old, unsupported and Buggy.

In the first days I just fixed some bugs and cleaned the codes in the TemplateLite sources.
After that I wanted to know how much faster and "lighter" TemplateLite than Smarty2 and Smarty3.
So I've created some test templates with foreach, section, config file, includes and a lot of assigns from PHP.
Because Smarty3 is backward compatible with Smarty2 and TemplateLite is based on Smarty2 the three template engine can use the same tpl files.

After some tests I've noticed that the main bottleneck is the slow IO and the slow file operations.
If I move the template resources to memcache or something else I need to give up the power of OpCaches.

The builtin OpCache do some filesystem caching and OptIns but it wasn't enough for me :)

In the latest 7.0.4 version I found the solution: the opcache_is_script_cached() and opcache_compile_file() functions

Smarty do a lot of file_exists and filemtime() queries to the filesystem to validate the compiled resource against the tpl file.
So If you fetch a compiled template the Zend OpCaches saves the binary opcodes into the memory.
If you modify the compiled resource file the OpCache invalidates the currently cached opcode.
With the opcache_is_script_cached() function I can make a function that tells me that the compiled resource file is modified since the last use or not.
But I need to compare the tpl file against the compiled resource. So with opcache_compile_file() I can move the tpl files to the OpCache and if the tpl file modified the OpCache invalidates the cached one.
So If I check the tpl file with the opcache_is_script_cached() function and it gives me a big false I check the filemtimes and if needed recompile the tpl file into php and with the opcache_compile_file function i can move the tpl file into the cache again.

In the TemplateLite sources I replaced the filemtime based comparsions with this OpCache based hack and TemplateLite gives me a 100% speed boost!
If I use a lot of concurent connections in the ab (apache benchmark) smarty gets extremly slower but with this hack isnt :)

in this repo you can find my TemplateLite fork patched with this hack.
