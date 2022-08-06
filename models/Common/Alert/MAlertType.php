<?php
namespace Rehike\Model\Common\Alert;

/**
 * Alert type definitions
 * 
 * Information: Blue, with a ✱ icon
 * Warning: Yellow, with a △ icon,
 * Error: Red, with an ! icon
 * Success: Blue (formerly green), with a ✓ icon
 */
abstract class MAlertType {
    const Information = "info";
    const Warning = "warn";
    const Error = "error";
    const Success = "success";
}