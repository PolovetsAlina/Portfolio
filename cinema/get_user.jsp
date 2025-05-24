<%@ page language="java" contentType="text/html; charset=UTF-8" pageEncoding="UTF-8"%>
<%@ page import="java.sql.*" %>
<%@ page import="org.json.JSONObject" %>
<%
    String username = request.getParameter("username");
    if (username == null || username.isEmpty()) {
        response.setStatus(400);
        JSONObject error = new JSONObject();
        error.put("success", false);
        error.put("message", "Username non specificato");
        out.println(error.toString());
        return;
    }

    Connection conn = null;
    PreparedStatement pstmt = null;
    ResultSet rs = null;
    
    try {
        Class.forName("com.mysql.cj.jdbc.Driver");
        conn = DriverManager.getConnection("jdbc:mysql://localhost:3306/cinema", "root", "");
        
        String sql = "SELECT username, nome, cognome, telefono, codice_fiscale, ruolo FROM utenti WHERE username = ?";
        pstmt = conn.prepareStatement(sql);
        pstmt.setString(1, username);
        rs = pstmt.executeQuery();
        
        if (rs.next()) {
            JSONObject response = new JSONObject();
            response.put("success", true);
            
            JSONObject data = new JSONObject();
            data.put("username", rs.getString("username"));
            data.put("nome", rs.getString("nome"));
            data.put("cognome", rs.getString("cognome"));
            data.put("telefono", rs.getString("telefono"));
            data.put("codice_fiscale", rs.getString("codice_fiscale"));
            data.put("ruolo", rs.getString("ruolo"));
            
            response.put("data", data);
            out.println(response.toString());
        } else {
            response.setStatus(404);
            JSONObject error = new JSONObject();
            error.put("success", false);
            error.put("message", "Utente non trovato");
            out.println(error.toString());
        }
    } catch (Exception e) {
        response.setStatus(500);
        JSONObject error = new JSONObject();
        error.put("success", false);
        error.put("message", e.getMessage());
        out.println(error.toString());
    } finally {
        try { 
            if (rs != null) rs.close(); 
        } catch (Exception e) {}
        try { 
            if (pstmt != null) pstmt.close(); 
        } catch (Exception e) {}
        try { 
            if (conn != null) conn.close(); 
        } catch (Exception e) {}
    }
%>
