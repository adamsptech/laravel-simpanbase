<?php

namespace App\Policies;

use App\Models\Task;
use App\Models\User;

class TaskPolicy
{
    /**
     * Determine if the user can view any tasks.
     */
    public function viewAny(User $user): bool
    {
        // All authenticated users can view tasks list
        return true;
    }

    /**
     * Determine if the user can view the task.
     */
    public function view(User $user, Task $task): bool
    {
        $role = $user->role?->name;

        // Engineers can only see tasks assigned to them
        if ($role === 'Engineer') {
            return $task->assigned_to === $user->id;
        }

        // All other roles can view any task
        return true;
    }

    /**
     * Determine if the user can create tasks.
     */
    public function create(User $user): bool
    {
        $role = $user->role?->name;

        // Only Admin and Planner can create tasks
        return in_array($role, ['Admin', 'Planner']);
    }

    /**
     * Determine if the user can update the task.
     */
    public function update(User $user, Task $task): bool
    {
        $role = $user->role?->name;

        // Admin and Planner can always edit
        if (in_array($role, ['Admin', 'Planner'])) {
            return true;
        }

        // Engineer can update if assigned to them and task is Open
        if ($role === 'Engineer') {
            return $task->assigned_to === $user->id && $task->status === Task::STATUS_OPEN;
        }

        // Supervisor can access tasks pending their approval
        if ($role === 'Supervisor' && $task->status === Task::STATUS_SUBMITTED_SUPERVISOR) {
            return true;
        }

        // Manager can access tasks pending their approval
        if ($role === 'Manager' && $task->status === Task::STATUS_SUBMITTED_MANAGER) {
            return true;
        }

        // Customer can access tasks pending their approval
        if ($role === 'Customer' && $task->status === Task::STATUS_SUBMITTED_CUSTOMER) {
            return true;
        }

        return false;
    }

    /**
     * Determine if the user can delete the task.
     */
    public function delete(User $user, Task $task): bool
    {
        $role = $user->role?->name;

        // Only Admin can delete
        return $role === 'Admin';
    }

    /**
     * Determine if the user can submit for supervisor approval.
     */
    public function submitForApproval(User $user, Task $task): bool
    {
        $role = $user->role?->name;

        // Engineer can submit when task is Open and assigned to them
        if ($role === 'Engineer' && $task->assigned_to === $user->id && $task->status === Task::STATUS_OPEN) {
            return true;
        }

        // Planner can submit on behalf of engineer
        if ($role === 'Planner' && $task->status === Task::STATUS_OPEN) {
            return true;
        }

        return false;
    }

    /**
     * Determine if the user can approve as supervisor.
     */
    public function approveAsSupervisor(User $user, Task $task): bool
    {
        $role = $user->role?->name;

        // Supervisor can approve when status is pending supervisor
        return $role === 'Supervisor' && $task->status === Task::STATUS_SUBMITTED_SUPERVISOR;
    }

    /**
     * Determine if the user can approve as manager.
     */
    public function approveAsManager(User $user, Task $task): bool
    {
        $role = $user->role?->name;

        // Manager can approve when status is pending manager
        return $role === 'Manager' && $task->status === Task::STATUS_SUBMITTED_MANAGER;
    }

    /**
     * Determine if the user can give final approval as customer.
     */
    public function approveAsCustomer(User $user, Task $task): bool
    {
        $role = $user->role?->name;

        // Customer can approve when status is pending customer
        return $role === 'Customer' && $task->status === Task::STATUS_SUBMITTED_CUSTOMER;
    }

    /**
     * Determine if the user can reject the task.
     */
    public function reject(User $user, Task $task): bool
    {
        $role = $user->role?->name;

        // Supervisor can reject if pending supervisor approval
        if ($role === 'Supervisor' && $task->status === Task::STATUS_SUBMITTED_SUPERVISOR) {
            return true;
        }

        // Manager can reject if pending manager approval
        if ($role === 'Manager' && $task->status === Task::STATUS_SUBMITTED_MANAGER) {
            return true;
        }

        // Customer can reject if pending customer approval
        if ($role === 'Customer' && $task->status === Task::STATUS_SUBMITTED_CUSTOMER) {
            return true;
        }

        return false;
    }
}
