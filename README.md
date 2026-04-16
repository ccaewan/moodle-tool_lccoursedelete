# Course Delete Trigger (Lifecycle plugin)

A lifecycle trigger that targets frozen (archived) courses for deletion based on long inactivity and age.

## Trigger logic

A course is selected for deletion when **all** of the following are true:

- The course context is **locked** (already archived/frozen)
- The course was created more than `creationdelay` ago (default: 60 months)
- The most recent enrolled-user access is older than `inactivitydelay` (default: 48 months), **or** the course has never been accessed at all

## Installation

This plugin should be installed at `admin/tool/lccoursedelete`.

## Dependencies

- [tool_lifecycle](https://moodle.org/plugins/tool_lifecycle) — requires the refactored subplugins API (PR #293)

## Configuration

| Setting | Default | Description |
|---|---|---|
| Inactivity delay | 48 months | Minimum inactivity period before a frozen course is eligible for deletion |
| Course creation delay | 60 months | Minimum course age before it is eligible for deletion |
