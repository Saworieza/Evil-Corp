<table class="table">
 @foreach ($fields as $field)
  <tr>
   <td><% $field->name %></td>
   <td>@if( isset($values[$field->name]) )
        @if( $field->data_type !== 'location')
         <% $values[$field->name] %>
        @else
         <% $values[$field->name]['img'] %>
        @endif
       @endif
   </td>
  </tr>
 @endforeach
</table>
