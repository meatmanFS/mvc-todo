<div class="jumbotron">
	<div class="add-task-wrap">
		<h2><?php echo $page_title; ?></h2>
		<?php if( !empty( $error ) ): ?>
			<p><?php echo $error; ?></p>
		<?php endif; ?>
		<hr />
		<div class="card">
			<div class="card-body">
				<form method="POST" action="/tasks" >
					<div class="form-group <?php echo (!empty($task_name_err)) ? 'has-error' : ''; ?>">
						<label for="task_name">Name</label>
						<input type="text" class="form-control" name="task_name" placeholder="Task name" value="<?php echo $task_name; ?>">
						<span class="help-block"><?php echo $task_name_err; ?></span>
					</div>								
					<div class="form-group <?php echo (!empty($task_end_date_err)) ? 'has-error' : ''; ?>">
						<label for="task_name">Due date</label>
						<input type="datetime-local" class="form-control" name="task_end_date" placeholder="Task End date" value="<?php echo $task_end_date; ?>">
						<span class="help-block"><?php echo $task_end_date_err; ?></span>
					</div>	
					<div class="form-group <?php echo (!empty($task_project_err)) ? 'has-error' : ''; ?>">
						<label for="task_project">Select project</label>
						<?php if( !empty( $projects ) ): ?>		
							<select class="form-control" name="task_project">
							<?php foreach ( $projects as $project ): ?>				
							<option value="<?php echo $project->get_id(); ?>" <?php echo ( $task_project == $project->get_id() )? 'selected="selected"': false; ?>><?php echo $project->name; ?></option>
							<?php endforeach; ?>
							</select>
						<?php else: ?>		
						<p>No project found, please add one <a href="/projects/create">here</a></p>
						<?php endif; ?>	
						<span class="help-block"><?php echo $task_project_err; ?></span>
					</div>
					<div class="form-group <?php echo (!empty($task_priority_err)) ? 'has-error' : ''; ?>">
						<label for="task_priority">Select priority</label>
						<select class="form-control" name="task_priority">
							<option value="1" 
								<?php echo ( $task_priority == 1 ) ? 'selected="selected"': false; ?>
							>Low</option>
							<option value="2"
								<?php echo ( $task_priority == 2 ) ? 'selected="selected"': false; ?>										
							>Middle</option>
							<option value="3"
								<?php echo ( $task_priority == 3 ) ? 'selected="selected"': false; ?>																				
							>High</option>
						</select>
						<span class="help-block"><?php echo $task_priority_err; ?></span>
					</div>
					<button type="submit" class="btn btn-primary">Add</button>
					<button type="reset" class="btn btn-secondary">Cancel</button>
				</form>		
			</div>
		</div>

	</div>
</div> 
