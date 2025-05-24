<%@ page language="java" contentType="text/html; charset=UTF-8" pageEncoding="UTF-8"%>
<%@ page import="java.sql.Connection" %>
<%@ page import="java.sql.DriverManager" %>
<%@ page import="java.sql.PreparedStatement" %>
<%@ page import="java.sql.ResultSet" %>
<%@ page import="java.sql.SQLException" %>
<%@ page import="javax.servlet.http.HttpSession" %>

<%
    // Verifica se l'utente è loggato
    if (session == null || session.getAttribute("username") == null) {
        response.sendRedirect("index.jsp");
        return;
    }
    
    String username = (String) session.getAttribute("username");
    String role = (String) session.getAttribute("role");
    if (!"admin".equals(role)) {
        response.sendRedirect("area_riservata.jsp");
        return;
    }

    String code = request.getParameter("proiezione_id");
    
    Connection conn = null;
    PreparedStatement pstmt = null;
    ResultSet rs = null;
    
    try {
        Class.forName("com.mysql.cj.jdbc.Driver");
        conn = DriverManager.getConnection("jdbc:mysql://localhost:3306/cinema", "root", "");
        
        String sql = "SELECT pr.*, f.Titolo FROM proiezioni pr, film f " +
                   "WHERE pr.Codice_film = f.Codice_film " +
                   "AND pr.Codice_proiezioni = ?";
        pstmt = conn.prepareStatement(sql);
        pstmt.setString(1, code);
        rs = pstmt.executeQuery();
        
        if (rs.next()) {
%>
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modifica Proiezione - Cinema Hub</title>
    <link rel="stylesheet" href="index.css">
</head>
<body>
    <main class="admin-panel">
        <h1>Modifica Proiezione</h1>
        
        <form action="updateProjection.jsp" method="post" class="admin-form">
            <input type="hidden" name="Codice_proiezioni" value="<%= code %>">
            
            <div class="form-group">
                <label for="filmCode">Film</label>
                <select id="filmCode" name="Codice_film" required class="form-control">
                    <%
                        // Get all films
                        String sqlFilms = "SELECT Codice_film, Titolo FROM film ORDER BY Titolo";
                        pstmt = conn.prepareStatement(sqlFilms);
                        ResultSet films = pstmt.executeQuery();
                        
                        while(films.next()) {
                            String filmCode = films.getString("Codice_film");
                            String filmTitle = films.getString("Titolo");
                            boolean isSelected = filmCode.equals(rs.getString("Codice_film"));
                    %>
                    <option value="<%= filmCode %>" <%= isSelected ? "selected" : "" %>>
                        <%= filmTitle %>
                    </option>
                    <%
                        }
                    %>
                </select>
            </div>
            
            <div class="form-group">
                <label for="date">Data</label>
                <input type="date" id="date" name="Data" value="<%= rs.getString("Data") %>" required class="form-control">
            </div>
            
            <div class="form-group">
                <label for="time">Ora</label>
                <input type="time" id="time" name="Ora" value="<%= rs.getString("Ora") %>" required class="form-control">
            </div>

            <button type="submit" class="admin-btn">Salva Modifiche</button>
        </form>
    </main>
</body>
</html>
<%
        } else {
            out.println("Proiezione non trovata.");
        }
    } catch (Exception e) {
        out.println("Errore: " + e.getMessage());
    } finally {
        try { if (rs != null) rs.close(); } catch (Exception e) { }
        try { if (pstmt != null) pstmt.close(); } catch (Exception e) { }
        try { if (conn != null) conn.close(); } catch (Exception e) { }
    }
%>
