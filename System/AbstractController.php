<?php

namespace UseBB\System;

use UseBB\System\Plugins\PluginRunningClass;

/**
 * Abstract controller class.
 * 
 * A controller handles requests which have been forwarded by the navigation 
 * system. Since controllers are a PluginRunningClass which are a 
 * ServiceAccessor, plugins can be called and all other services be used, such
 * as database, input, etc.
 * 
 * \author Dietrich Moerman
 */
abstract class AbstractController extends PluginRunningClass {}
