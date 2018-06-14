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

### Failing tests
You can find the screenshots of failing tests on the `files/screenshots` folder

### @todo
- [ ] make field mapping work
- [ ] make overrides for image fields work
- [x] make overrides for content fields work
- [ ] make yml overrides for image fields work
- [x] make yml overrides for content fields work
- [ ] make custom overrides for image fields work
- [x] make custom overrides for content fields work
- [ ] make url aliases work on nodes, terms and images
- [ ] fix php errors when running tests
- [ ] allow to have more than one alias for the same field name
- [ ] update readme