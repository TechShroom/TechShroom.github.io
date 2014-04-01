---
layout: post
title: Tips for Minecraft Modding
---
Here are some tips for modding in Minecraft, valid as of version 1.7.2:

1. Don't think world.isRemote means that you are on the server, it's the opposite. A better name would be world.isClient.
2. In containers, the progress bar updates are for passible values between your server tile entity and your client tile entity, make sure to use them or your tile entity will be out of sync!
3. When performing logic, do it on the server by wrapping it in a `if (world.isRemote)` and then sync your changes to the client. The client should be only receiving updates and rendering them.
4. __More to come!__
