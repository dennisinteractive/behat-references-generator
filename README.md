# Behat References Generator
Generates non-existing referenced entities when creating content

### Installation
1. `composer require dennisdigital/behat-references-generator:dev-master`

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

2. Edit behat.yml and add the contexts and configuration following the example in [behat.yml.dist]: https://github.com/marcelovani/behat-references-generator/blob/master/behat.yml.dist


### Requirements
You need to enable these modules
- Entity API

**For the demo you will need to enable this module:**
- Node Reference Content (Copy from the fixtures folder into the site's modules folder)

### Running
- Go into the tests folder
- Run `./behat --format=pretty`

### Failing tests
You can find the screenshots of failing tests on the `files/screenshots` folder

### @todo
- [x] make field mapping work
- [ ] make default content for image fields work
- [x] make default content for node fields work
- [ ] make yml overrides for image fields work
- [x] make yml overrides for content fields work
- [ ] make step definition overrides for image fields work
- [x] make step definition overrides for content fields work
- [ ] make url aliases work on nodes, terms and images
- [ ] fix php errors when running tests
- [ ] allow to have more than one alias for the same field name
- [ ] support default content for terms
- [ ] support content generation for terms
- [ ] update readme
