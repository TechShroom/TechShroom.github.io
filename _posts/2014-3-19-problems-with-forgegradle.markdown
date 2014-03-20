---
layout: post
title: Problems with ForgeGradle, anyone?
---
Recently I've been starting up with Forge again. ForgeGradle is hard to work with. If you stumble across this looking for help, many of the helpful links I've extracted are below. I've also included some tips.

Tips (check back for updates):
* If you want source, run <code>gradle setupDecompWorkspace</code>, go to <code>~/.gradle/caches/minecraft/net/minecraftforge/forge/&lt;version&gt;/</code> and extract the contents of the <code>forgeSrc-&lt;version&gt;-source.jar</code> to a folder in your Forge project folder. Then just add the extracted files as source, and you're good to go!

Links:
* [Modding With Forge and Eclipse](http://www.minecraftforum.net/topic/2413773-)
* [Getting Started With ForgeGradle](http://www.minecraftforge.net/forum/index.php/topic,14048.0.html)
* [Forge and IntelliJ and Scala](http://minalien.com/tutorial-setting-up-forgegradle-for-intellij-idea-scala/)
