# Source code folder

This folder stores most non-PHP source code used in Rehike, as well as miscellaneous source files, such as images.

## Building

You need Node.js.

Run the following command to install dependencies:

```bash
npm install --include=dev
```

Then to build all packages, run:

```bash
cd ./build_tools

rehikebuild
```

To build packages individually, use the `--package` or `-p` parameter as such:

```bash
rehikebuild --package js/rebug css/rebug
```

On Windows hosts, it is recommended to use a Bash shell (such as Git CLI) to run the commands.