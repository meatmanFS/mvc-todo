<div class="jumbotron">
	<div class="add-task-wrap">
		<h2>
			Add new task
		</h2>
		<hr />
		<div class="card">
			<div class="card-body">
				<form method="POST" action="/tasks" >
					<div class="form-group">
						<label for="task_name">Name</label>
						<input type="text" class="form-control" name="task_name" placeholder="Task name">
					</div>	
					<div class="form-group">
						<label for="task_name">Due date</label>
						<input type="datetime-local" class="form-control" name="task_end_date" placeholder="Task End date">
					</div>	
					<div class="form-group">
						<label for="task_project">Select project</label>						
						<?php if( !empty( $projects ) ): ?>		
							<select class="form-control" name="task_project">
							<?php foreach ( $projects as $project ): ?>				
							<option value="<?php echo $project->get_id(); ?>"><?php echo $project->name; ?></option>
							<?php endforeach; ?>
							</select>
						<?php else: ?>		
						<p>No project found, please add one <a href="/projects/create">here</a></p>
						<?php endif; ?>		
					</div>
					<div class="form-group">
						<label for="task_priority">Select priority</label>
						<select class="form-control" name="task_priority">
							<option value="1">Low</option>
							<option value="2">Middle</option>
							<option value="3">High</option>
						</select>
					</div>
					<button type="submit" class="btn btn-primary">Add</button>
					<button type="reset" class="btn btn-secondary">Cancel</button>
				</form>		
			</div>
		</div>

	</div>
</div> 
