# Behat References Generator
Generates non-existing referenced entities when creating content

### Installation
`composer require dennisdigital/behat-references-generator`

You might need to add it to repositories first
```
"repositories": {
    "behat-references-generator": {
      "type": "vcs",
      "url": "git@github.com:marcelovani/behat-references-generator.git",
      "no-api": true
    }
.
.
.
```

### Requirements
You need to enable these modules
- Entity API
- Node Reference Content (Copy from the fixtures folder into the site's modules folder)

### Running
- Go into the tests folder
- Run `./behat --format=pretty`
