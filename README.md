# Gearman conveyor
Gearman (gearman.org) based module of distributed execution and control tasks allowing to handle tasks in assistence with [Process&Load Management](https://github.com/jamset/process-load-manager)(Pm&Lm) and [Tasks inspector](https://github.com/jamset/tasks-inspector) module.

##Install

`composer require jamset/german-conveyor`

##Description

The architecture can be divided into three parts:

1) Client/clients

2) Gearman server

3) Workers

####The client part consists of:
1) Console command that starts client by CRON and that contain class extended BaseGearmanCLient. 
Where parameters of Pm&Lm and worker's command name are set.

####Server part:
1) gearmand (Gearman job server)
[Read how to install needed libs here](href)

####Workers side:

1) Worker-manager. Worker that contains the process Manager (Pm&Lm) that performs task with addTaskHigh() level
http://php.net/manual/en/gearmanclient.addtaskhigh.php

2) Worker-performer. Worker containing Service performing the final (goal) work with normal or low level [handling tasks added to server by addTask() or addTaskLow() commands]
http://php.net/manual/en/gearmanclient.addtask.php 
http://php.net/manual/en/gearmanclient.addtasklow.php

####Main logic (abstract)

CRON launch the client. Then in the client initializes the DTO, which set in the client tasks as tasks that 
must be accepted by the Service (worker-performer) for the final run. 

After all tasks for a service is set initializes parameters of the Pm&Lm and DTO, that is passed to Gearman Server 
as the task with high level (which means that it will be executed before all others with a lower priority level)

Then there is a ->runTasks() which puts the client in the standby mode (to listen callbacks, with errors or just with execution state info)

So, tasks are initialized and transfered to Gearman Server (where they become jobs for workers).

After that (periodically) CRON runs the console command of worker-manager. I.e. 
[Pm&Lm](https://github.com/jamset/process-load-manager) manager or other (Note: if worker-manager doesn't receive any task for it from Gearman Server it terminates).

The Gearman Server passes that Pm&Lm script task, containing DTO for Pm&Lm.
 
Pm starts to create processes under the control of the Lm. 

These processes are workers that contains the Service (worker-performer). 
 
When it creates process worker-performer level this worker accesses the Gearman server and obtains the Gearman task as job. 
And such job contains DTO with parameters for the service: ids or some other useful information.

I.e. ids of Google Analytics campaigns when every task (job) assume calling Google Analytics API, handling result and work with
database.

At the completion each worker-performer sends to the client (by callback through Gearman server) the message about the 
completion of task (job). 

This message contains the type of ExecutionDto, which indicates whether an error is exist (usual or critical; 
when a critical error force client to run die() ) [, error's text, text of correct execution.]

In case of an error, the client re-creates those tasks that came back with an error, and again writes them to the 
queue for execution.

Then when the next worker-manager (Pm&Lm) will start by CRON and create child processes (worker-performers) 
one of them will take the repeated task and try to perform it.

At a time when the same client attempts to start by CRON, whether he starts or not depends on the property of client's class
with name executionType: CONSISTENT (default) or PARALLEL. 

If the selected scenario here "CONSISTENT", then the same client can start only after will be completed the previous one. 
If "PARALLEL", it will run whatever works the same client.

I.e. when you want to delegate control on all tasks to one client you have to use default value. When you want to use
multiple tasks (i.e. for long-term execution of complex work) you have to use PARALLEL option.

###Features:

- It allows to choose process management system and use Gearman callbacks to handle messages containing information
about task's execution status and re-init tasks if needed, log or send information about execution by 
[TasksInspector](https://github.com/jamset/tasks-inspector) module

##Schema

On the schema Gearman Conveyor module presents with [PublisherPulsar](https://github.com/jamset/publisher-pulsar) module

![Gearman conveyor](https://github.com/jamset/gearman-conveyor/raw/master/images/gearman-conveyor.jpg)
