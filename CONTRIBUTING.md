# For Rehike Contributors

**Hello! Thanks for checking out Rehike.**

Rehike is a project that attempts to restore the classic YouTube layout, known as Hitchhiker. We welcome whatever help we can get.

## What can I help with?

As the project grows, it becomes harder for a single person to maintain everything. We encourage you to help contributing however you can.

We don't bite and we'll assist with changes rather than sharply reject changes that we have issues with, so please don't be shy!

### **I don't know how to code.**

That's okay! You can still make valuable contributions to the project.

For example, if you speak multiple languages, you can contribute to our internationalisation (i18n) and translate the project's strings to your language.

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
- **There is no strict style guide to adhere to**, however, please try to imitate preexisting code and don't go too crazy.
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
