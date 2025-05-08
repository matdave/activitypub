# MODX ActivityPub

This plugin will turn your site into an [ActivityPub](https://activitypub.rocks/) server.

## Server

Your site is the server, and you can use it to send and receive activity streams.

## Actor

Your MODX Users are actors on this server. They are identified by their username.

Alternatively, the MODX site can be set up in "single user mode" so that all post are attributed to a single/shared user.

Available actor types: https://w3c.github.io/activitystreams/vocabulary/#actor-types

## Activity Stream

A detailed list of links to a users interaction options. Generally, this is retrieved by requesting the profile page with the `Accept: application/activity+json` header

### Outbox
The outpost is a paginated collection of federated posts. MODX Resources can be made into a post by a user in the Activity Pub tab of the resource.

Depending on the type of resource and interaction they can appear differently, e.g. https://www.w3.org/TR/activitystreams-vocabulary/#activity-types

#### Likes/Shares
Interaction count related to an item in the Outbox

#### Replies
Paginated collection of replies made to an article

### Followers
Followers are a list of user IDs (e.g. https://otherserver/users/followername) that are following the Actor

### Inbox
Used for handling incoming interactions with Actor, e.g. post comments, follow actor

### Dispatch
This isn't an endpoint but the hidden glue that makes it all work. The server will dispatch events to other activity pub compliant servers. This will most generally be the following:
- Send new post to followers
- Send ACCEPT response to inbox follow requests

## .well-known

The .well-known folder is the common standard for hosting two primary endpoints for federation: 
 - webfinger - The endpoint used to get information about an actor and links to more data
    - E.g. `example.com/.well-known/webfinger?resource=acct:actor@server.com`
    - E.g. `example.com/.well-known/webfinger?resource=https://server.com/actor`
    - Live E.g. https://floss.social/.well-known/webfinger?resource=acct:matdave@floss.social
 - nodeinfo - The endpoint used to get information about the server instance
    - Live E.g. [https://floss.social/.well-known/nodeinfo](https://floss.social/nodeinfo/2.0)

## References

- https://github.com/landrok/activitypub
- https://www.w3.org/TR/activitypub/
- https://github.com/Automattic/wordpress-activitypub
- https://git.drupalcode.org/project/activitypub
- https://seb.jambor.dev/posts/understanding-activitypub/
- https://verify.funfedi.dev
- https://github.com/smolblog/smolblog/tree/main/packages/framework/activitypub/src/Signatures

# Requirements

PHP >= 8.1

# Attribution

Signature verification is based on the hard work of the [smolblog](https://github.com/smolblog/smolblog/) project.