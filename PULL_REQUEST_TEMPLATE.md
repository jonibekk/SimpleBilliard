Connected to this Jira issue
https://jira.goalous.com/browse/GL-****

## Pre-Review Checklist
### Basic Requirements
- [ ] The code diff of the PR is appropriate size, not huge
- [ ] Meets specification
- [ ] Merge destination is correct
- [ ] Code has been reformatted
- [ ] No typos
- [ ] Create test cases if the PR doesn't depend on other task

### Client Side
- [ ] JS has been tested in strict mode
- [ ] Tested on IE11
- [ ] Tested all pages with changed code
- [ ] No errors in internet browser's console log
- [ ] If CSS class definition was changed, ensure no error in all instances after refactoring
- [ ] If JS variable was renamed, ensure no error in all instances after refactoring 
- [ ] If JS method and/or its arguments were renamed, ensure no error in all instances after refactoring 
- [ ] UI review by other developers or product owner is OK

### Server Side
- [ ] Follow [Coding Guideline](https://confluence.goalous.com/x/qoPT)
- [ ] No error in Cake & debug log
- [ ] If model was edited, update its test case
- [ ] If a variable was renamed, ensure no error in all instances after refactoring 
- [ ] If a method and/or its arguments were renamed, ensure no error in all instances after refactoring
- [ ] If an endpoint was changed, update its documentations in Postman
- [ ] DB migration files are combined to one if the PR include the file of that type 

## Checklist after merged 
- [ ] Delete branch if the branch is unnecessary
- [ ] Do [these procedures](https://confluence.goalous.com/x/KgEQAQ) definitely for release version management if merged to `master` or `master-isao branch`

## Important pages related PR
https://confluence.goalous.com/x/SQEQAQ
