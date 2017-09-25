<% if $Tags %>
  <div class="tags">
    <span class="with"><%t Tags_ss.TAGGEDWITH 'Tagged with' %></span>
    <ul>
      <% loop $Tags %>
        <li class="tag"><a href="$Link">$Title</a></li>
      <% end_loop %>
    </ul>
  </div>
<% end_if %>
