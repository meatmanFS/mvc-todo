<div class="jumbotron">
	<div class="add-task-wrap">
		<?php if( !empty( $error ) ): ?>
			<p><?php echo $error; ?></p>
		<?php endif; ?>
		<hr />
		<div class="card">
			<div class="card-body">
				<form method="POST" action="/projects" >
					<div class="form-group <?php echo (!empty($project_name_err)) ? 'has-error' : ''; ?>">
						<label for="project_name">Name</label>
						<input type="text" class="form-control" name="project_name" placeholder="Project name" value="<?php echo $project_name; ?>">
						<span class="help-block"><?php echo $project_name_err; ?></span>
					</div>			
					<div class="form-group <?php echo (!empty($project_color_err)) ? 'has-error' : ''; ?>">
						<label for="project_color">Project color</label>
						<input type="color"  class="form-control" name="project_color"  value="<?php echo $project_color; ?>" />
						<span class="help-block"><?php echo $project_color_err; ?></span>
					</div>					
					<button type="submit" class="btn btn-primary">Add</button>
					<button type="reset" class="btn btn-secondary">Cancel</button>
				</form>		
			</div>
		</div>

	</div>
</div> 
