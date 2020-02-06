# Patches

The tool [`patch-package`](https://github.com/ds300/patch-package) allows to override coding in dependencies.

## `@lerna/version` and `@lerna/conventional-commits`

This patch allows to pass a `conventionalChangelog` object in `lerna.json`. See also this: https://github.com/lerna/lerna/pull/2343

You can customize the changelog generation ([reference](https://github.com/conventional-changelog/conventional-changelog/tree/master/packages/conventional-changelog-core#conventionalchangelogcoreoptions-context-gitrawcommitsopts-parseropts-writeropts)) in your `lerna.json`:

```json
{
  "command": {
    "version": {
      "conventionalCommits": true,
      "conventionalChangelog": {
        "options": {},
        "context": {},
        "gitRawCommitsOpts": {},
        "parserOpts": {},
        "writerOpts": {}
      }
    }
  }
},
```
