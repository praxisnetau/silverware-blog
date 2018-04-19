<% if $Archive %>
  <ul class="years">
    <% loop $Archive %>
      <li>
        <a href="$Link" class="year"><span class="text">$Year</span><% if $Top.ShowTotals %> <span class="total">($Total)</span><% end_if %></a>
        <ul class="months">
          <% loop $Months %>
            <li><a href="$Link" class="month"><span class="text">$Month</span><% if $Top.ShowTotals %> <span class="total">($Total)</span><% end_if %></a></li>
          <% end_loop %>
        </ul>
      </li>
    <% end_loop %>
  </ul>
<% else %>
  <% include Alert Type='warning', Text=$NoDataMessage %>
<% end_if %>
