<?php
namespace Rehike\Constants;

/*
 * DEVELOPER NOTICE:
 * 
 * Be careful modifying this file. Especially in the case of
 * changing or removing an option.
 * 
 * Make sure to check if these changes will not break anything.
 * 
 * Thank you.
 */

/**
 * Enables GitHub integration with some Rehike features.
 * 
 * @var bool
 */
const GH_ENABLED = true;

/**
 * Specifies the GitHub repository to link to.
 * 
 * @var string
 */
const GH_REPO = "Rehike/Rehike";

/** 
 * The current version of Rehike.
 * 
 * @var string
 */
const VERSION = "0.8.3";
const VERSION_MAJOR_INT = 0;
const VERSION_MINOR_INT = 8;
const VERSION_SUB_INT   = 3;

/**
 * Is the current Rehike build a release build?
 * 
 * @var bool
 */
const IS_RELEASE = false;

/** 
 * The location of views (templates) relative to the root. 
 * 
 * @var string
 */
const VIEWS_DIR = "template/hitchhiker";