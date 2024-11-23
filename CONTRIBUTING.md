# For Rehike Contributors

**Hello! Thanks for checking out Rehike.**

Rehike is a project that attempts to restore the classic YouTube layout, known as Hitchhiker. We welcome whatever help we can get.

## What can I help with?

As the project grows, it becomes harder for a single person to maintain everything. We encourage you to help contributing however you can.

We don't bite and we'll assist with changes rather than sharply reject changes that we have issues with, so please don't be shy!

### **I don't know how to code.**

That's okay! You can still make valuable contributions to the project.

For example, if you speak multiple languages, you can contribute to our internationalization (i18n) and translate the project's strings to your language.

Just as you can report a bug through the issues tab, you can also make a suggestion that the developers can consider.

### **I want to contribute code.**

There are many things you can help with in code contribution.

- **Search for `TODO`'s or `BUG`'s.** We consider this an important chore to do, as these things tend to be left for months without being touched. If something needs to be done, don't be afraid to help do it.
    - ```php
        // TODO(author): Example of a TODO notice.

        // BUG(author): Example of a BUG notice.
      ```
- **Look at the issues.** We value community feedback on the project and use this system to report bugs or recommendations. This oughta give you an idea on what can be done.
- **Add documentation.** If you see any of our code as difficult to understand or confusing, it can be valuable to add clarification in comments.

## Code standards

There are a few standards that we wish for all contributors to abide by to ensure clean, readable, maintainable, flexible, and stable code. Here are the main points:

- **Rehike is a multi-paradigm project.** We don't enforce a procedural or object-oriented code style, as we believe both work better in tandem.
    - However, for **procedural code**—code that operates without creating object instances—**we do require that classes are still used to wrap the code.** This is due to a PHP limitation; the autoloader cannot be used for non-class structures. This is also why a majority of our classes consist solely of static functions.
- **Our style guide isn't super strict**, but please follow these common guidelines:
    - Place braces for code blocks on new lines, except when they are part of an expression. An expression in PHP is basically anything that needs a semicolon after it (but grammatically it's a bit more complicated than that, for example, anything inside an = sign assignment operation is part of an expression, so our rule applies in all these cases). Example:
      ```php
      // This is a class declaration. It is not part of an expression, so the
      // brace goes on a new line.
      class ClassDefinition
      {
          // Likewise, this class method declaration is not an expression, so
          // brace on new line.
          public function methodDefinition(): void
          {
              // This if expression is also not an expression, so new line.
              if (expression == true)
              {
                  // This object array cast is part of an assignment expression,
                  // so the block opening token goes on the same line.
                  $obj = (object)[
                      "key" => "value"
                  ];

                  // This is a function expression. In PHP, these actually work
                  // quite a bit different from regular functions, so there is
                  // somewhat of a significance to distinguish them in syntax.
                  // (but really Taniko is just petty)
                  $cb = function() use ($obj) {
                      // This is a match expression. This language feature is only
                      // implemented as an expression, so sometimes we break the
                      // rule. But, for the sake of demonstration, the brace is
                      // inlined here too.
                      return match ($obj->key) {
                          "value" => true,
                          default => false
                      };
                  };
              }
          }
      }
      ```
    - Surround code blocks (function declarations, if/while/for, etc.) with newlines, excluding the tops and bottoms of scopes. Comments relating to a specific code block should precede the code block, without surrounding spaces, or be at the top of the body of the block. The former style is preferred for non-chained blocks, whereas the latter style is preferred for chained blocks, such as if/else.
      ```php
      /**
       * Comment describing the functionality of this method.
       */
      public function exampleMethod(): string
      {
          if ($this->whatever)
          {
              return "hi!";
          }

          $a = 0;

          // Increment $a until it is 100.
          while ($a < 100)
          {
              $a++;
          }

          if ($a == 100)
          {
              // Return "100".
              return (string)$a;
          }
          else
          {
              // Return "wtf!!" if this somehow happens.
              return "wtf!!";
          }
      }
      ```
    - Use `PascalCase` names for user-defined types (classes, enums, and the sort); `camelCase` names for function, class methods, and variables; and `CONSTANT_CASE` for all constants and enum cases.
    - Use `else if` instead of `elseif`, except in the alternate PHP formatting used for HTML templates (with the colon instead of braces for blocks), where it is required.
    - Use `//` for single-line comments instead of `#`.
    - Please try to fit your code within some range between 80 and 100 characters.
    - Though we do like to use `BUG` in comments to mark code that needs to be fixed. `FIXME` may not be checked for as much, so please stay away from using it.
- **Understand the separation of worlds.**
    - For example, data should seldom be modified in HTML templates. This helps maintain structured and easy to understand code. While it may be tempting to get it done with in one place, misuse of an environment can create a maintenance nightmare down the line.
    - **Modules** implement the backend of Rehike. Anything that doesn't directly influence a page's construction (i.e. anything global) should be implemented as a module.
    - **Controllers** are used to coordinate general behaviour of a page.
    - **Models** are used to (re)structure data for sending to the HTML templater. Separating the structuring of data (models) from the representation of data (HTML) helps keep both clean.
    - **Templates** are used to represent data in HTML documents; to convert a model to HTML. These are written in Twig, rather than directly implemented in PHP.
- **Update the `.version` file before you leave.** Fortunately, you don't need to do this manually!
    - We've included a `.git-hooks` folder in the project. This contains a `pre-push` file. Just copy this to your `.git/hooks` folder in this project and it will automatically increment the version number when you push an update.

Thank you for reading this! Should you choose to contribute to this project, your work will be appreciated.

The Rehike Team
