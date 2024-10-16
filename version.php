<?php

/**
 * Learning Book plugin version info
 *
 * This file contains the version information for the Learning Book module.
 * It defines the required Moodle version and provides the component name,
 * plugin version, and the cron execution frequency.
 *
 * @package     mod_learningbook // Package name (unique identifier of the plugin)
 * @author      A S M IRFAN // Author of the plugin
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later // License under which the plugin is distributed
 */

defined('MOODLE_INTERNAL') || die; // Prevents direct access to this file via URL.

$plugin->component = 'mod_learningbook'; // Full name of the plugin (used for diagnostics and during installation).
$plugin->version   = 2024101602; // The current version of the plugin (Date format: YYYYMMDDXX, where XX is a version increment for that day).
$plugin->requires  = 2021120100; // The minimum Moodle version required for this plugin (based on Moodle release number).
$plugin->maturity  = MATURITY_ALPHA;
$plugin->release   = 'v0.1';
$plugin->cron      = 0;          // How often Moodle cron will check this plugin for updates (0 means it doesn't need to be checked).
