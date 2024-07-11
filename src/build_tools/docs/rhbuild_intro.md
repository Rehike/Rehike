# Introduction to RehikeBuild

**RehikeBuild** is a build system for Rehike (creative name, we know). It is based on the Gulp task runner and Node.js.

## Packages and `.rhbuild` files

Packages in RehikeBuild are defined by folders in the `src/` folder with `.rhbuild` files in them. Every such folder corresponds to a package, and packages typically have a one-to-one correspondence to build tasks. There may be exceptions to this rule in the future, but it is the case for the design of RehikeBuild.

`.rhbuild` files specify metadata for generating a build task.

You can build individual packages by passing the `--package` parameter to the command-line interface.