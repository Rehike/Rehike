# For Rehike Contributors

Hello! Thanks for checking out Rehike.

Rehike is a project that attempts to restore the classic YouTube layout, known as Hitchhiker. We welcome whatever help we can get.

However, there are a few standards that we wish for all contributors to abide by to ensure clean, readable, maintainable, flexible, and stable code. Here are the main points:

- Rehike takes advantage of object-oriented design in PHP. This provides a few advantages over procedural design in PHP.
 - **Please do not write fully functional code.** That means, any set of functions not wrapped in a class. If you're modifying the application source code at all, please stick to writing static classes instead. Otherwise, your code will not load at all since PHP's autoloader only supports classes.
 - Classes should be designed in adherence to [PSR-4](https://www.php-fig.org/psr/psr-4/). This just means that classes are accessed via their **filesystem path**. Declare the namespace to be all the parent directories of the file, and the class name to be the same as the file. This is required by the autoloader.
- There is no strict style guide to adhere to, however, please try to imitate preexisting code and don't go too crazy. <sup>Looking at you there, Taniko, with all your braces on new lines and backwards if statements :P</sup>
- **Please do NOT attempt to modify data in Twig**. Twig is the language we use for HTML templates. It is only meant for *parsing* data and forming HTML out of it. Data should only be modified in the PHP environment directly.
 - In fact, this means that you should try to maintain some structure in general. Getting everything done in one file may be tempting - it is quick, after all - but it's going to cost us when it inevitably breaks.

Thank you for reading this! Should you choose to contribute to this project, your work will be appreciated.

The Rehike Team