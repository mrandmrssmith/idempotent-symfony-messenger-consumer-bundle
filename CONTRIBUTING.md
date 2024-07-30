# Welcome to the Mr & Mrs Smith contributing guide

Thank you for investing your time in contributing to our project! 

:exclamation: Please keep our community approachable and respectable.

In this guide you will get an overview of the contribution workflow from opening an issue, creating a PR, reviewing, and merging the PR.

## Getting started

### Coding Standards

PSR-12, OOP, SOLID, Symfony

### Tests

Please create new or update any and all unit tests.

### Documentation

Please create new or update any documentation which reflects changes to current behaviour.

### Development

#### Building environment

Docker will help with development of this project and a Dockerfile is provided.

:grey_exclamation: **Please see the `Makefile` for all the helper commands to get you up and running!**

#### Debugging

Instructons to follow, but `xdebug` IS installed

```shell

```

#### Code Linting & Testing

:grey_exclamation: **Please see the `Makefile` for all the helper commands to get you up and running!**


### Issues

#### Create a new issue

If you spot a problem, search if an issue [already exists](https://github.com/mrandmrssmith/git@github.com:mrandmrssmith/symfony-messenger-json-serializer/issues). If a related issue doesn't exist, you can open a new issue.

#### Solve an issue

Scan through our [existing issues](https://github.com/mrandmrssmith/git@github.com:mrandmrssmith/symfony-messenger-json-serializer/issues) to find one that interests you. As a general rule, we don’t assign issues to anyone. If you find an issue to work on, you are welcome to open a PR with a fix.

### Make Changes

#### Make changes locally

1. Fork the repository.
   - Using the command line:
       - [Fork the repo](https://docs.github.com/en/github/getting-started-with-github/fork-a-repo#fork-an-example-repository) so that you can make your changes without affecting the original project until you're ready to merge them.
2. Create a working branch and start with your changes!

### Commit your update

Commit the changes once you are happy with them. Don't forget to self-review, to speed up the review process:zap:.

#### Self review

You should always review your own PR first.

- [ ] Review the content for technical accuracy.
- [ ] If there are any failing checks in your PR, troubleshoot them until they're all passing.

### Pull Request

When you're finished with the changes, create a pull request, also known as a PR.
- Fill the "Ready for review" template so that we can review your PR. This template helps reviewers understand your changes as well as the purpose of your pull request.
- Don't forget to [link PR to issue](https://docs.github.com/en/issues/tracking-your-work-with-issues/linking-a-pull-request-to-an-issue) if you are solving one.
- Enable the checkbox to [allow maintainer edits](https://docs.github.com/en/github/collaborating-with-issues-and-pull-requests/allowing-changes-to-a-pull-request-branch-created-from-a-fork) so the branch can be updated for a merge.
  Once you submit your PR, a team member will review your proposal. We may ask questions or request additional information.
- We may ask for changes to be made before a PR can be merged, either using [suggested changes](https://docs.github.com/en/github/collaborating-with-issues-and-pull-requests/incorporating-feedback-in-your-pull-request) or pull request comments. You can apply suggested changes directly through the UI. You can make any other changes in your fork, then commit them to your branch.
- As you update your PR and apply changes, mark each conversation as [resolved](https://docs.github.com/en/github/collaborating-with-issues-and-pull-requests/commenting-on-a-pull-request#resolving-conversations).
- If you run into any merge issues, checkout this [git tutorial](https://github.com/skills/resolve-merge-conflicts) to help you resolve merge conflicts and other issues.

### Your PR is merged!

Congratulations :tada::tada: The Mr & Mrs Smith team thanks you :sparkles:.
