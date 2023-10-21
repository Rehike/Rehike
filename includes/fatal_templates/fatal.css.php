<?php ?>
<style>
html, body
{
    min-height: 100vh;
}

body
{
    margin: 0;
    font-family: Roboto, Arial, Helvetica, sans-serif;
    font-size: 13px;
    background: #f1f1f1;
    display: flex;
    justify-content: center;
    align-items: center;
}

body, input, button, textarea, select
{
    font-family: Roboto, Arial, Helvetica, sans-serif;
}

#rehike-fatal-error
{
    width: 800px;
    padding: 15px;
    margin: 0 auto;
    background: #fff;
    box-shadow: 0 1px 2px rgba(0,0,0,.1);
    -moz-box-sizing: border-box;
    box-sizing: border-box;
}

.header > *
{
    vertical-align: middle;
}

.header h1
{
    display: inline;
}

.section-header
{
    font-weight: 500;
}

.fatal-button
{
    display: inline-block;
    height: 28px;
    border: solid 1px transparent;
    padding: 0 10px;
    outline: 0;
    font-weight: 500;
    font-size: 11px;
    text-decoration: none;
    white-space: nowrap;
    word-wrap: normal;
    line-height: normal;
    vertical-align: middle;
    cursor: pointer;
    border-radius: 2px;
    box-shadow: 0 1px 0 rgba(0,0,0,0.05);

    border-color: #d3d3d3;
    background: #f8f8f8;
    color: #333;
}

.failed-request-info .section-title
{
    font-weight: 500;
}

.failed-request-text
{
    font-family: Consolas, monospace;
}

<?php
// Error log text styling:
?>
ul.fatal-error-info
{
    list-style: none;
}

.fatal-error-info .section-title
{
    font-weight: bold;
}

.fatal-error-info li
{
    margin-bottom: 3px;
}

.no-message
{
    color: #666;
    font-style: italic;
}

.exception-log
{
    word-wrap: break-word;
    white-space: pre-wrap;
    white-space: -moz-pre-wrap;
    white-space: -pre-wrap;
    white-space: -o-pre-wrap;
}

.exception_class
{
    font-weight: bold;
}

.at_text
{
    color: #666;
}

.class_name, .double_colon_operator
{
    color: #990080;
}

.method_name, .function_name
{
    font-style: italic;
    font-weight: bold;
    color: #99112c;
}

.file_line_parentheses, .file_line
{
    color: #666;
}

.args_type_object
{
    color: #8a2be2;
}

.args_type_string
{
    color: #008000;
}

.args_type_number
{
    color: #1f47e3;
}

.args_type_resource, .args_type_null, .args_type_unknown, .args_type_array
{
    color: #333;
}

</style>